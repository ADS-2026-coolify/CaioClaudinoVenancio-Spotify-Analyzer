import axios from 'axios';

export const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
  withCredentials: true,
});

apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    // Check if error is 401 unauthorized
    if (error.response?.status === 401) {
      // Allow caller components or auth context to handle redirection
    }
    return Promise.reject(error);
  }
);
