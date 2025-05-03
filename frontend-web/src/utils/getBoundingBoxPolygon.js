export function getBoundingBoxPolygon(points = []) {
  if (points.length < 2) return [];

  const lats = points.map(([, lat]) => lat);
  const lngs = points.map(([lng]) => lng);

  const north = Math.max(...lats);
  const south = Math.min(...lats);
  const east = Math.max(...lngs);
  const west = Math.min(...lngs);

  return [
    [north, west],
    [north, east],
    [south, east],
    [south, west],
    [north, west], // tutup loop
  ];
}
