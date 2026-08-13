FROM python:3.12-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

# Uvicorn, HTTP на 8000 (за nginx). Порт наружу не выставляем — только через прокси.
CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8000"]
