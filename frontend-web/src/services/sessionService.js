import axios from "axios";

const API = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL, // Ganti dengan base API Laravel kamu
  headers: {
    "Content-Type": "application/json",
  },
});

export const getSessionSummary = async (sessionId) => {
  const response = await API.get(`/session/${sessionId}/summary`);
  return response.data;
};

export const getSessionDateRange = async (start_date, end_date) => {
  const response = await API.get(
    `/tracking-summary?start_date=${start_date}&end_date=${end_date}`
  );
  return response.data;
};

export const getSessionPoints = async (sessionId) => {
  const response = await API.get(`/session/${sessionId}/points`);
  return response.data;
};

export const getActiveSession = async (machineId) => {
  const response = await API.get(`/machine/${machineId}/active-session`);
  return response.data;
};
