import axios from "axios";

const BASE = import.meta.env.VITE_API_BASE_URL;

export async function getAllMachines() {
  const res = await axios.get(`${BASE}/machine`);
  return res.data?.data || [];
}
