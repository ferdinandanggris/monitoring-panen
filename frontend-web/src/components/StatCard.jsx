import React from "react";

export default function StatCard({ title, value }) {
  return (
    <div className="bg-white/30 backdrop-blur-md border border-dark/40 p-4 rounded-md shadow-lg text-dark">
      <div className="text-sm text-dark/70">{title}</div>
      <div className="text-xl font-semibold">{value}</div>
    </div>
  );
}
