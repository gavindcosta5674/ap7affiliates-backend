from app import app, build_tracking_url
from fastapi.testclient import TestClient


def test_tracking_url_uses_requested_domain():
    url = build_tracking_url('AFF-1001', 'instagram', 'summer')
    assert 'http://ap7affiliates.online/' in url
    assert 'aff=AFF-1001' in url


def test_raw_data_endpoint_returns_list():
    client = TestClient(app)
    response = client.get('/api/raw-data')
    assert response.status_code == 200
    assert isinstance(response.json(), list)


def test_import_csv_text_endpoint_adds_rows():
    client = TestClient(app)
    response = client.post('/api/raw-data/import-csv', json={
        'csv_text': 'DateTime,Deposit,Withdraw,Bonus,Player\n2026-06-29 10:00,200,0,10,alice'
    })

    assert response.status_code == 200
    payload = response.json()
    assert payload['success'] is True
    assert payload['imported_count'] == 1
    assert payload['rows'][0]['player_username'] == 'alice'
