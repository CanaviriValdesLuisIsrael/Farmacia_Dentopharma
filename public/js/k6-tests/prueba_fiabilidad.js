import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '1m', target: 20 },
    { duration: '1m', target: 20 },
    { duration: '30s', target: 0 },
  ],
};

const BASE = 'http://127.0.0.1:8000';

export default function () {
  const login = http.post(`${BASE}/`, {
    email: 'admin@correo.com',
    password: '123456',
    _token: 'csrf-bypass',
  });
  check(login, {
    'login sin error 500': (r) => r.status !== 500,
  });
  sleep(1);

  const dash = http.get(`${BASE}/admin/dashboard`);
  check(dash, { 'dashboard sin error 500': (r) => r.status !== 500 });
  sleep(1);

  const prod = http.get(`${BASE}/admin/adm_producto`);
  check(prod, { 'productos sin error 500': (r) => r.status !== 500 });
  sleep(1);

  const lotes = http.get(`${BASE}/admin/lotes`);
  check(lotes, { 'lotes sin error 500': (r) => r.status !== 500 });
  sleep(1);

  const ventas = http.get(`${BASE}/admin/ventas`);
  check(ventas, { 'ventas sin error 500': (r) => r.status !== 500 });
  sleep(1);
}