import React from "react";

export default function SummaryView({ technicians, dateRange }) {
  return (
    <div className="p-6 overflow-y-auto h-full">
      <h3 className="text-xl font-semibold mb-4">📊 Ringkasan Data</h3>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div className="bg-white shadow rounded p-4">
          <div className="text-gray-500 text-sm">Total Teknisi</div>
          <div className="text-2xl font-bold">{technicians.length}</div>
        </div>
        <div className="bg-white shadow rounded p-4">
          <div className="text-gray-500 text-sm">Tanggal Mulai</div>
          <div className="text-md">
            {dateRange[0].startDate.toLocaleDateString()}
          </div>
        </div>
        <div className="bg-white shadow rounded p-4">
          <div className="text-gray-500 text-sm">Tanggal Akhir</div>
          <div className="text-md">
            {dateRange[0].endDate.toLocaleDateString()}
          </div>
        </div>
      </div>

      {/* Placeholder Chart */}
      <div className="bg-white shadow rounded p-6">
        <p className="text-gray-700 text-sm mb-2">📈 Grafik Performa</p>
        <div className="h-40 bg-gray-100 rounded flex items-center justify-center text-gray-400">
          (Chart di sini)
        </div>
      </div>
    </div>
  );
}