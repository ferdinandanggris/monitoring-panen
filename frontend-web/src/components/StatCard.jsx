export default function StatCard({ title, value }) {
  return (
    <div className="bg-white/20 backdrop-blur-md text-white border border-teal-300/40 rounded-xl p-4 shadow">
      <div className="text-sm text-teal-100">{title}</div>
      <div className="text-2xl font-bold">{value}</div>
    </div>
  );
}