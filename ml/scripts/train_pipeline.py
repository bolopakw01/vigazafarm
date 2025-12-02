"""Template training pipeline for the DSS recommender.

Fill each function once historical data is available. Keeping the logic in
Python scripts ensures the same transformations run locally and in CI.
"""

from __future__ import annotations

from dataclasses import dataclass
from pathlib import Path
from typing import Any

import pandas as pd  # type: ignore

ARTIFACT_DIR = Path(__file__).resolve().parents[1] / "artifacts"
DATA_DIR = Path(__file__).resolve().parents[1] / "data"


@dataclass
class TrainingConfig:
    target: str = "feed_efficiency_label"
    test_size: float = 0.2
    random_state: int = 42


def load_data() -> pd.DataFrame:
    """Load raw exports into a DataFrame.

    Replace the placeholder implementation with real file loading logic
    (CSV/Parquet/database dump). Ensure sensitive sources stay outside Git.
    """

    raise NotImplementedError("Provide data loading logic once exports exist")


def engineer_features(df: pd.DataFrame) -> tuple[pd.DataFrame, pd.Series]:
    """Split the incoming frame into features and label/target series."""

    raise NotImplementedError("Design domain features before training")


def train_model(features: pd.DataFrame, target: pd.Series) -> Any:
    """Train and return a fitted model object."""

    raise NotImplementedError("Select and fit a model (e.g., XGBoost, SVM, etc.)")


def persist_model(model: Any, config: TrainingConfig) -> Path:
    """Persist the trained model to disk for the Laravel service to consume."""

    ARTIFACT_DIR.mkdir(parents=True, exist_ok=True)
    destination = ARTIFACT_DIR / "dss_model.onnx"
    raise NotImplementedError(f"Serialize {model=} to {destination}")


def run(config: TrainingConfig) -> None:
    df = load_data()
    features, target = engineer_features(df)
    model = train_model(features, target)
    persist_model(model, config)


if __name__ == "__main__":
    run(TrainingConfig())
