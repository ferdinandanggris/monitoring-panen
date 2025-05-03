import { useEffect, useRef, useState } from "react";

export default function usePolling(callback, interval = 3000) {
  const savedCallback = useRef();
  const [active, setActive] = useState(true); // 👀 state untuk pause/resume

  useEffect(() => {
    savedCallback.current = callback;
  }, [callback]);

  useEffect(() => {
    const handleVisibilityChange = () => {
      setActive(document.visibilityState === "visible");
    };

    document.addEventListener("visibilitychange", handleVisibilityChange);
    return () => {
      document.removeEventListener("visibilitychange", handleVisibilityChange);
    };
  }, []);

  useEffect(() => {
    if (!active) return;

    const id = setInterval(() => {
      savedCallback.current?.();
    }, interval);

    return () => clearInterval(id);
  }, [interval, active]);
}
