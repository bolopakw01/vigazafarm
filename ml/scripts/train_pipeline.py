"""Template training pipeline for the DSS recommender.

Fill each function once historical data is available. Keeping the logic in
Python scripts ensures the same transformations run locally and in CI.
"""

from __future__ import annotations

from dataclasses import asdict, dataclass
from pathlib import Path
from typing import Any

import json

import pandas as pd  # type: ignore

ARTIFACT_DIR = Path(__file__).resolve().parents[1] / "artifacts"
DATA_DIR = Path(__file__).resolve().parents[1] / "data"


@dataclass
class TrainingConfig:
    target: str = "biaya_per_butir"
    test_size: float = 0.2
    random_state: int = 42


def load_data() -> pd.DataFrame:
    """Load the packaged 180-day quail dataset.

    This uses ``ml/data/dataset_puyuh_180_hari.csv`` as a lightweight
    "training" source so that the Laravel side can detect a trained
    artifact and surface a model version.
    """

    data_file = DATA_DIR / "dataset_puyuh_180_hari.csv"
    if not data_file.exists():
        raise FileNotFoundError(
            f"Dataset not found at {data_file}. "
            "Pastikan file sudah diekspor ke ml/data/."
        )

    df = pd.read_csv(data_file)
    if df.empty:
        raise ValueError(f"Dataset at {data_file} is empty")

    return df


def engineer_features(df: pd.DataFrame) -> tuple[pd.DataFrame, pd.Series]:
    """Split the incoming frame into features and label/target series.

    Untuk sementara, kita tidak melakukan feature engineering rumit.
    Model "ringan" ini hanya menyimpan statistik dasar agar pipeline
    training dapat berjalan end-to-end.
    """

    if TrainingConfig.target not in df.columns:
        raise KeyError(
            f"Target column {TrainingConfig.target!r} tidak ditemukan "
            f"di dataset. Kolom tersedia: {list(df.columns)}"
        )

    target = df[TrainingConfig.target].astype(float)
    features = df.drop(columns=[TrainingConfig.target])
    return features, target


def train_model(features: pd.DataFrame, target: pd.Series) -> Any:
    """Train and return a fitted model object.

    Alih-alih menggunakan library ML berat, kita membangun model
    statistik sederhana yang menyimpan:
    - jumlah sampel
    - rata-rata target
    - rata-rata tiap fitur numerik
    """

    numeric_features = features.select_dtypes(include=["number"])

    model: dict[str, Any] = {
        "type": "statistical-baseline",
        "n_samples": int(target.shape[0]),
        "target_mean": float(target.mean()),
        "feature_means": {
            col: float(numeric_features[col].mean())
            for col in numeric_features.columns
        },
    }

    return model


def persist_model(model: Any, config: TrainingConfig) -> Path:
    """Persist the trained model to disk for the Laravel service to consume."""

    ARTIFACT_DIR.mkdir(parents=True, exist_ok=True)
    destination = ARTIFACT_DIR / "dss_model.onnx"

    payload = {
        "config": asdict(config),
        "model": model,
        "metadata": {
            "format": "json-inside-onnx-shell",
            "note": "Placeholder artifact for Laravel DSS ML integration.",
        },
    }

    with destination.open("w", encoding="utf-8") as f:
        json.dump(payload, f, indent=2)

    return destination


def run(config: TrainingConfig) -> None:
    df = load_data()
    features, target = engineer_features(df)
    model = train_model(features, target)
    persist_model(model, config)


if __name__ == "__main__":
    run(TrainingConfig())
