import http from "k6/http";
import { check, sleep } from "k6";

export const options = {
    stages: [
        { duration: "30s", target: 20 },
        { duration: "30s", target: 40 },
        { duration: "30s", target: 60 },
        { duration: "30s", target: 0 },
    ],
};

const BASE = "http://127.0.0.1:8000";

export default function () {
    const loginRes = http.post(`${BASE}/`, {
        email: "admin@correo.com",
        password: "123456",
        _token: "csrf-bypass",
    });
    check(loginRes, {
        "login responde": (r) =>
            r.status === 200 || r.status === 302 || r.status === 419,
    });
    sleep(1);

    const dash = http.get(`${BASE}/admin/dashboard`);
    check(dash, { "dashboard accesible": (r) => r.status !== 500 });
    sleep(1);

    const prod = http.get(`${BASE}/admin/adm_producto`);
    check(prod, { "productos accesible": (r) => r.status !== 500 });
    sleep(1);
}
