const colors = [
  "teal",
  "red",
  "blue",
  "green",
  "orange",
  "purple",
  "magenta",
  "brown",
  "gold",
];

export default function getPolylineColor(id) {
  return colors[id % colors.length]; // misal pakai id 1,2,3,...
}
