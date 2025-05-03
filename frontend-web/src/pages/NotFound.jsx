import React from "react";

export default function NotFound() {
  return (
    <div className="flex items-center justify-center h-screen bg-gray-100 mx-5">
      <div className="text-center p-8 bg-white shadow-md rounded-md">
        <h1 className="text-4xl font-bold text-green-700 mb-4">404</h1>
        <p className="text-lg text-gray-700 mb-2">Halaman tidak ditemukan</p>
        <p className="text-sm text-gray-500">
          Pastikan URL sudah benar atau kembali ke dashboard.
        </p>
      </div>
    </div>
  );
}
