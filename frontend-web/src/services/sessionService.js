import axios from "axios";

const API = axios.create({
  baseURL: "http://localhost:8000/api", // Ganti dengan base API Laravel kamu
  headers: {
    "Content-Type": "application/json",
  },
});

export const getSessionSummary = async (sessionId) => {
  const response = await API.get(`/session/${sessionId}/summary`);
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
