export default function SessionTable({ points }) {
  return (
    <div className="bg-white/20 backdrop-blur-md text-white rounded-lg shadow-lg p-4">
      <h2 className="text-lg font-semibold mb-2">📋 Titik Koordinat</h2>
      <div className="overflow-x-auto">
        <table className="w-full table-auto text-sm">
          <thead>
            <tr className="bg-white/10 text-teal-100">
              <th className="p-2 text-left">#</th>
              <th className="p-2 text-left">Latitude</th>
              <th className="p-2 text-left">Longitude</th>
              <th className="p-2 text-left">Speed</th>
              <th className="p-2 text-left">Fuel</th>
            </tr>
          </thead>
          <tbody>
            {points?.map((point, i) => (
              <tr key={i} className="border-t border-white/10">
                <td className="p-2">{i + 1}</td>
                <td className="p-2">{point.latitude}</td>
                <td className="p-2">{point.longitude}</td>
                <td className="p-2">{point.speed}</td>
                <td className="p-2">{point.fuel}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
