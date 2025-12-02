"""Minimal inference harness.

Once a model artifact exists, replace the placeholder logic with a real loader
(e.g., ONNXRuntime, TorchScript). Keeping a CLI utility here makes it easy to
smoke-test predictions before touching Laravel.
"""

from __future__ import annotations

from dataclasses import dataclass
from pathlib import Path
from typing import Any

ARTIFACT_PATH = Path(__file__).resolve().parents[1] / "artifacts" / "dss_model.onnx"


@dataclass
class InferenceRequest:
    phase: str
    metrics: dict[str, float]


def load_model() -> Any:
    if not ARTIFACT_PATH.exists():
        raise FileNotFoundError(
            "No trained model found. Run ml/scripts/train_pipeline.py once data is ready."
        )
    raise NotImplementedError("Load and return the trained artifact")


def predict(model: Any, request: InferenceRequest) -> dict[str, Any]:
    raise NotImplementedError("Implement real inference logic")


def main() -> None:
    raise SystemExit(
        "This is a stub. Train a model and update predict_stub.py to run local smoke tests."
    )


if __name__ == "__main__":
    main()
