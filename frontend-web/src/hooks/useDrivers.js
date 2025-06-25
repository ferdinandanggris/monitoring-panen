import { useEffect, useState } from "react";
import { getAllDrivers } from "../services/driverService";

export default function useDrivers() {
  const [drivers, setDrivers] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getAllDrivers().then((data) => {
      setDrivers(data);
      setLoading(false);
    });
  }, []);

  const refetch = ()=>{
    getAllDrivers().then((data) => {
      setDrivers(data);
      setLoading(false);
    });
  }

  return { drivers, loading , refetch};
}
