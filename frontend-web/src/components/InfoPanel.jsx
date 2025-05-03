export default function InfoPanel({ driver, distance, area, cost }) {
  return (
    <div className="bg-white/20 backdrop-blur-md shadow-lg rounded p-4 border border-teal-200/40 text-sm space-y-1 text-white w-full">
      <h2 className="font-semibold text-white mb-2">📋 Informasi Panen</h2>
      <div><span className="font-medium">Sopir:</span> {driver}</div>
      <div><span className="font-medium">Total Luas:</span> {area} m²</div>
      <div><span className="font-medium">Total Jarak:</span> {distance} m</div>
      <div><span className="font-medium">Biaya:</span> Rp {cost}</div>
    </div>
  );
}