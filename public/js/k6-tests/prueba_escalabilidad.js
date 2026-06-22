import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '30s', target: 10 },
    { duration: '30s', target: 20 },
    { duration: '30s', target: 30 },
    { duration: '30s', target: 40 },
    { duration: '30s', target: 50 },
    { duration: '30s', target: 0 },
  ],
};

const BASE = 'http://127.0.0.1:8000';

export default function () {
  const loginRes = http.post(`${BASE}/`, {
    email: 'admin@correo.com',
    password: '123456',
    _token: 'csrf-bypass',
  });
  check(loginRes, {
    'login responde': (r) => r.status === 200 || r.status === 302 || r.status === 419,
  });
  sleep(1);

  const dash = http.get(`${BASE}/admin/dashboard`);
  check(dash, { 'dashboard accesible': (r) => r.status !== 500 });
  sleep(1);

  const lotes = http.get(`${BASE}/admin/lotes`);
  check(lotes, { 'lotes accesible': (r) => r.status !== 500 });
  sleep(1);

  const prov = http.get(`${BASE}/admin/proveedor`);
  check(prov, { 'proveedores accesible': (r) => r.status !== 500 });
  sleep(1);
}