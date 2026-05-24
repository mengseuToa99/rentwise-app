import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE_URL = __ENV.BASE_URL || 'http://127.0.0.1:8000';

export const options = {
  vus: Number(__ENV.VUS || 200),
  duration: __ENV.DURATION || '2m',
  thresholds: {
    http_req_failed: ['rate<0.02'],
    http_req_duration: ['p(95)<1000'],
  },
};

export default function () {
  const responses = http.batch([
    ['GET', `${BASE_URL}/`],
    ['GET', `${BASE_URL}/login`],
    ['GET', `${BASE_URL}/dashboard`],
  ]);

  check(responses[0], {
    'home status is 200/302': (r) => r.status === 200 || r.status === 302,
  });

  check(responses[1], {
    'login status is 200/302': (r) => r.status === 200 || r.status === 302,
  });

  check(responses[2], {
    'dashboard status is 200/302': (r) => r.status === 200 || r.status === 302,
  });

  sleep(Number(__ENV.SLEEP || 0.4));
}
