// import turbo
import * as Turbo from "@hotwired/turbo";

// ===== GLOBAL INIT FUNCTION =====
function initApp() {
    initCharts();
    initRegionDropdown();
    initOtherComponents();
}

// ===== CHART =====
function initCharts() {
    const seg = document.getElementById('segmentasiChart');
    if (!seg) return;

    // destroy chart lama kalau ada (ANTI DUPLIKAT)
    if (window.segmentChart) {
        window.segmentChart.destroy();
    }

    window.segmentChart = new Chart(seg, {
        type: 'doughnut',
        data: {
            labels: window.segmentLabels || [],
            datasets: [{
                data: window.segmentData || [],
                backgroundColor: ['#033e87', '#01a850', '#f59e0b', '#ef4444']
            }]
        }
    });

    const lay = document.getElementById('layananChart');
    if (!lay) return;

    if (window.layananChart) {
        window.layananChart.destroy();
    }

    window.layananChart = new Chart(lay, {
        type: 'bar',
        data: {
            labels: window.serviceLabels || [],
            datasets: [{
                data: window.serviceData || [],
                backgroundColor: '#01a850'
            }]
        }
    });
}

// ===== REGION DROPDOWN =====
function initRegionDropdown() {
    const kab = document.getElementById('kabupaten');
    const kec = document.getElementById('kecamatan');
    const kel = document.getElementById('kelurahan');

    if (!kab || !window.regionMap) return;

    kab.addEventListener('change', function () {
        kec.innerHTML = '<option value="">Semua Kecamatan</option>';
        kel.innerHTML = '<option value="">Semua Kelurahan</option>';

        const data = window.regionMap[this.value] || {};

        Object.keys(data).forEach(k => {
            kec.innerHTML += `<option value="${k}">${capitalize(k)}</option>`;
        });
    });

    kec.addEventListener('change', function () {
        kel.innerHTML = '<option value="">Semua Kelurahan</option>';

        const data = window.regionMap[kab.value]?.[this.value] || [];

        data.forEach(k => {
            kel.innerHTML += `<option value="${k}">${capitalize(k)}</option>`;
        });
    });
}

// ===== HELPER =====
function capitalize(str) {
    return str.replace(/\b\w/g, c => c.toUpperCase());
}

// ===== TURBO HOOK =====
document.addEventListener("turbo:load", initApp);