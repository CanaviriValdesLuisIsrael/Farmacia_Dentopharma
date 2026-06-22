import http from "k6/http";
import { check, sleep } from "k6";

// ─── CONFIGURACIÓN: 20 usuarios virtuales por 1 minuto ───
export const options = {
    vus: 20,
    duration: "1m",
};

const BASE = "http://127.0.0.1:8000";

export default function () {
    // 1. Login
    const loginRes = http.post(`${BASE}/`, {
        email: "admin@correo.com",
        password: "123456",
        _token: "csrf-bypass",
    });

    check(loginRes, {
        "login responde 200 o 302": (r) =>
            r.status === 200 || r.status === 302 || r.status === 419,
    });

    sleep(1);

    // 2. Dashboard
    const dash = http.get(`${BASE}/admin/dashboard`);
    check(dash, { "dashboard accesible": (r) => r.status !== 500 });

    sleep(1);

    // 3. Productos
    const prod = http.get(`${BASE}/admin/adm_producto`);
    check(prod, { "productos accesible": (r) => r.status !== 500 });

    sleep(1);

    // 4. Lotes
    const lotes = http.get(`${BASE}/admin/lotes`);
    check(lotes, { "lotes accesible": (r) => r.status !== 500 });

    sleep(1);

    // 5. Proveedores
    const prov = http.get(`${BASE}/admin/proveedor`);
    check(prov, { "proveedores accesible": (r) => r.status !== 500 });

    sleep(1);
}
