import axios from "axios";

const BASE = import.meta.env.VITE_API_BASE_URL;

export async function getAllDrivers() {
  const res = await axios.get(`${BASE}/driver`);
  return res.data?.data || [];
}
