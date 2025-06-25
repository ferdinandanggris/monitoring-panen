import { useEffect, useState } from "react";
import { getAllMachines } from "../services/machineService";

export default function useMachines() {
  const [machines, setMachines] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getAllMachines().then((data) => {
      setMachines(data);
      setLoading(false);
    });
  }, []);

  const refetch = () => {
    getAllMachines().then((data) => {
      setMachines(data);
      setLoading(false);
    });
  }

  return { machines, loading, refetch };
}
