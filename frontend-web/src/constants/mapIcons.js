import L from "leaflet";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";

export const SmallCircleMarker = new L.Icon({
  iconUrl:
    "data:image/svg+xml;base64," +
    btoa(
      `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"><circle cx="5" cy="5" r="4" fill="teal"/></svg>`
    ),
  iconSize: [10, 10], // Ukuran kecil
  iconAnchor: [5, 5], // Titik tengah marker
  popupAnchor: [0, -5],
});

export const TinyDefaultMarker = new L.Icon({
  iconUrl: markerIcon,
  shadowUrl: markerShadow,
  iconSize: [15, 24], // ← Ukuran lebih kecil
  iconAnchor: [7, 24],
  popupAnchor: [1, -22],
});

export const TractorEmojiMarker = new L.DivIcon({
  className: "",
  html: `<div style="
    font-size: 20px;
    line-height: 1;
    text-shadow: 0 0 2px white;
  ">🚜</div>`,
  iconSize: [10, 10],
  iconAnchor: [8, 8],
  popupAnchor: [0, -12],
});
