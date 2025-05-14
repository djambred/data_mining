from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import joblib
import numpy as np
from datetime import datetime

app = FastAPI()

# Load models
clf = joblib.load("model_kemampuan_bayar.pkl")
reg_total = joblib.load("model_total_disetujui.pkl")
reg_lama = joblib.load("model_lama_pinjaman.pkl")

# Request data model
class PredictionRequest(BaseModel):
    name: str
    age: int
    salary: float
    loan_amount: float
    loan_date: str
    payday: str

# Response data model
class PredictionResponse(BaseModel):
    predicted_kemampuan_bayar: int
    kemampuan_bayar_label: str
    total_pemberian_pinjaman: float
    lama_pinjaman: int

@app.post("/predict", response_model=PredictionResponse)
def predict(data: PredictionRequest):
    try:
        # Validate salary threshold
        if data.age < 21:
            raise HTTPException(
                status_code=400,
                detail="Age must be at least 21."
            )
        if data.salary < 1_000_000:
            raise HTTPException(
                status_code=400,
                detail="Salary must be at least 1,000,000 to make a prediction."
            )
        if data.loan_amount > 10_000_000:
            raise HTTPException(
                status_code=400,
                detail="Loan amount must not exceed 10,000,000."
            )

        # Parse dates
        loan_date = datetime.strptime(data.loan_date, "%Y-%m-%d")
        payday = datetime.strptime(data.payday, "%Y-%m-%d")
        if loan_date.year != payday.year or loan_date.month != payday.month:
            raise HTTPException(
                status_code=400,
                detail="Loan date and payday must be in the same month and year."
            )
        loan_duration_days = (payday - loan_date).days

        # Prepare input features for prediction
        input_features = np.array([[data.age, data.salary, data.loan_amount, loan_duration_days, 0]])  # Adjust if necessary

        # Predict class (ability to pay)
        pred_class = clf.predict(input_features)[0]

        # Predict total loan amount
        pred_total = reg_total.predict(input_features)[0]

        # Predict loan duration
        pred_lama = reg_lama.predict(input_features)[0]

        # Prepare human-readable label for 'kemampuan_bayar'
        kemampuan_bayar_label = "Mampu bayar" if pred_class == 1 else "Tidak mampu bayar"

        # Return predictions
        return {
            "predicted_kemampuan_bayar": int(pred_class),
            "kemampuan_bayar_label": kemampuan_bayar_label,
            "total_pemberian_pinjaman": int(pred_total),
            "lama_pinjaman": int(pred_lama)
        }

    except HTTPException as e:
        raise e
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Prediction failed: {e}")
