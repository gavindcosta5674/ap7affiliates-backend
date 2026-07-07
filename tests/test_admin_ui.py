from pathlib import Path


def test_admin_ui_uses_inr_and_shows_registration_date():
    html = Path('index.html').read_text(encoding='utf-8')

    assert 'Registration Date' in html
    assert "document.getElementById('stat-total-deposits').textContent = '₹' + numFormat(data.total_deposits);" in html
    assert "document.getElementById('comm-total-revenue').textContent = '₹' + numFormat(data.total_revenue);" in html


def test_admin_ui_has_raw_data_entry_form():
    html = Path('index.html').read_text(encoding='utf-8')

    assert 'Daily Raw Data' in html
    assert 'id="raw-data-form"' in html


def test_affiliate_ui_exposes_raw_data_view():
    html = Path('affiliate.html').read_text(encoding='utf-8')

    assert 'Daily Raw Data' in html
    assert 'id="raw-data-table-body"' in html
