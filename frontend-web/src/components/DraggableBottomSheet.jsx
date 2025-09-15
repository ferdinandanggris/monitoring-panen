/**
 * Props
 * - isOpen : bool
 * - onClose : () => void
 * - initialPercent : number
 * - minPercent : number
 * - closeThreshold : number
 */

import React, { useEffect, useMemo, useRef, useState } from "react";

export default function DraggableBottomSheet({
  isOpen,
  onClose,
  initialPercent = 0.5,
  minPercent = 0.2,
  closeThreshold = 0.1,
  initialPage = "home",
  children,
}) {
  // --- STATE & REF DASAR ---
  const [vh, setVh] = useState(0);
  const [percent, setPercent] = useState(initialPercent);
  const [isDragging, setIsDragging] = useState(false);

  //drag
  const startYRef = useRef(0);
  const startHeightPxRef = useRef(0);
  const draggingRef = useRef(false);

  // --- navigator sederhana (page + history) ---
  const [history, setHistory] = useState([initialPage]); // stack of page names
  const page = history[history.length - 1];
  const canGoBack = history.length > 1;

  const navigate = (nextPage) => setHistory((h) => [...h, nextPage]);
  const goBack = () => setHistory((h) => (h.length > 1 ? h.slice(0, -1) : h));

  // reset page & tinggi saat dibuka kembali
  useEffect(() => {
    if (isOpen) {
      setPercent(initialPercent);
      setHistory([initialPage]);
    }
  }, [isOpen, initialPercent, initialPage]);

  //height px percent (untuk style inline)
  const heightPx = useMemo(() => Math.round(vh * percent), [vh, percent]);

  // --- MEASURE VIEWPORT ----
  useEffect(() => {
    const measure = () => {
      setVh(window.innerHeight || document.documentElement.clientHeight);
    };
    measure();
    window.addEventListener("resize", measure);
    return () => window.removeEventListener("resize", measure);
  });

  // --- RESET TINGGI SAAT DIBUKA LAGI
  useEffect(() => {
    if (isOpen) setPercent(initialPercent);
  }, [isOpen, initialPercent]);

  // --- LOCK BODY SCROLL SAAT SHEET TERBUKA & ESC UNTUK CLOSE ---
  useEffect(() => {
    if (!isOpen) return;
    const prev = document.body.style.overflow;
    document.body.style.overflow = "hidden";

    const onKey = (e) => {
      if (e.key === "Escape") onClose?.();
      if (e.key === "Backspace" && canGoBack) {
        e.preventDefault();
        goBack();
      }
    };
    window.addEventListener("keydown", onKey);

    return () => {
      document.body.style.overflow = prev;
      window.removeEventListener("keydown", onKey);
    };
  }, [isOpen, onClose]);

  // ---- MULAI DRAG (hanya di handle) ----
  const onHandleDown = (e) => {
    if (!isOpen) return;
    setIsDragging(true);
    draggingRef.current = true;
    startYRef.current = "touches" in e ? e.touches[0].clientY : e.clientY;
    startHeightPxRef.current = heightPx;

    document.body.style.userSelect = "none"; // cegah blok teks saat drag
    window.addEventListener("pointermove", onMove);
    window.addEventListener("pointerup", onUp);
    window.addEventListener("touchmove", onMove, { passive: false });
    window.addEventListener("touchend", onUp);
  };

  // ---- SAAT DRAG BERJALAN ----
  const onMove = (e) => {
    if (!draggingRef.current) return;
    if ("cancelable" in e && e.cancelable) e.preventDefault();

    const currentY = "touches" in e ? e.touches[0].clientY : e.clientY;
    const delta = startYRef.current - currentY; // geser ke atas => delta positif
    const nextHeightPxRaw = startHeightPxRef.current + delta;

    // IZINKAN 0..vh saat drag (supaya bisa masuk zona close)
    const nextHeightPx = Math.min(vh, Math.max(0, nextHeightPxRaw));
    setPercent(nextHeightPx / vh);
  };

  // ---- LEPAS DRAG ----
  const onUp = () => {
    setIsDragging(false);

    draggingRef.current = false;
    document.body.style.userSelect = "";

    window.removeEventListener("pointermove", onMove);
    window.removeEventListener("pointerup", onUp);
    window.removeEventListener("touchmove", onMove);
    window.removeEventListener("touchend", onUp);

    // Jika ditarik cukup ke bawah -> tutup
    if (percent < closeThreshold) {
      onClose?.();
      return;
    }

    // Jika tidak ditutup, pastikan tidak lebih kecil dari minPercent
    if (percent < minPercent) {
      setPercent(minPercent);
    }
  };

  // --- RENDER ----
  return (
    <>
      {/* Overlay gelap */}
      <div
        className={`fixed inset-0 bg-black/40 transition-opacity
          ${
            isOpen
              ? "opacity-100 pointer-events-auto"
              : "opacity-0 pointer-events-none"
          }`}
        onClick={onClose}
      />

      {/* Sheet */}
      <div
        className={`fixed left-0 right-0 bottom-0 bg-white rounded-t-2xl shadow-2xl
           ${isDragging ? "" : "transition-[height,transform] duration-300"} 
          ${isOpen ? "translate-y-0" : "translate-y-full"}`}
        style={{ height: isOpen ? `${heightPx}px` : 0 }}
        onClick={(e) => e.stopPropagation()}
        role="dialog"
        aria-modal="true"
      >
        {/* Header sederhana: handle + tombol close */}
        <div className="relative pt-3 pb-2 select-none">
          {/* Handle: area untuk drag */}
          <div
            className="mx-auto h-1.5 w-12 rounded-full bg-gray-300 cursor-grab active:cursor-grabbing touch-none"
            onPointerDown={onHandleDown}
            onTouchStart={onHandleDown}
            aria-label="Drag handle"
          />

          {/* Tombol back (opsional, tetap simpel) */}
          {canGoBack && (
            <button
              type="button"
              onClick={goBack}
              className="absolute left-2 top-2 inline-flex items-center justify-center h-9 w-9 rounded-full  active:scale-95 transition"
              aria-label="Kembali"
              onPointerDown={(e) => e.stopPropagation()}
              onTouchStart={(e) => e.stopPropagation()}
            >
              {/* chevron left */}
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="size-6"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M15.75 19.5 8.25 12l7.5-7.5"
                />
              </svg>
            </button>
          )}

          {/* Tombol close (opsional, tetap simpel) */}
          <button
            type="button"
            onClick={onClose}
            className="absolute right-2 top-2 inline-flex items-center justify-center h-9 w-9 rounded-full border border-gray-200 bg-white shadow-sm hover:bg-gray-50 active:scale-95 transition"
            aria-label="Tutup"
            onPointerDown={(e) => e.stopPropagation()}
            onTouchStart={(e) => e.stopPropagation()}
          >
            <svg
              viewBox="0 0 24 24"
              className="h-5 w-5 text-gray-700"
              aria-hidden="true"
            >
              <path
                d="M6 6l12 12M18 6L6 18"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
              />
            </svg>
          </button>
        </div>

        {/* Konten: tinggi = total sheet minus header (sekitar 56px) */}
        <div className="h-[calc(100%-56px)] overflow-auto px-4 pb-4 pt-4">
          {typeof children === "function"
            ? children({ page, navigate, goBack, canGoBack })
            : children}
        </div>
      </div>
    </>
  );
}
