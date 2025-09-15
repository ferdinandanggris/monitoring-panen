import React, { useEffect } from "react";
import {
  MapContainer,
  TileLayer,
  Marker,
  Popup,
  Polyline,
  useMap,
} from "react-leaflet";
import L from "leaflet";
import getPolylineColor from "../utils/getPolylineColor";

function FitToBounds({ technicians }) {
  const map = useMap();

  useEffect(() => {
    if (!technicians || technicians.length === 0) return;

    const bounds = L.latLngBounds([]);

    technicians.forEach((tech) => {
      if (tech.position) bounds.extend(tech.position);
      if (tech.path && tech.path.length > 0)
        tech.path.forEach((pt) => bounds.extend(pt));
    });

    if (bounds.isValid()) {
      map.fitBounds(bounds, { padding: [50, 50] });
    }
  }, [technicians]);

  return null;
}

export default function MapView({ technicians }) {
  const defaultCenter = [-7.866315, 111.465607];

  const createIcon = (avatar) =>
    L.divIcon({
      className: "custom-div-icon",
      html: `<div style="
        background: white;
        border-radius: 50%;
        overflow: hidden;
        width: 32px;
        height: 32px;
        box-shadow: 0 0 5px rgba(0,0,0,0.3);
      ">
        <img src="${avatar}" style="width: 100%; height: 100%;" />
      </div>`,
      iconSize: [32, 32],
    });

  return (
    <MapContainer
      center={defaultCenter}
      zoom={13}
      scrollWheelZoom={true}
      className="w-full h-full"
    >
      <TileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" />

      <FitToBounds technicians={technicians} />

      {technicians.map((tech) => (
        <React.Fragment key={tech.id}>
          <Marker position={tech.position} icon={createIcon(tech.avatar)}>
            <Popup>
              <strong>{tech.name}</strong>
              <br />
              {tech.role}
            </Popup>
          </Marker>

          {tech.path && (
            <Polyline
              positions={tech.path}
              pathOptions={{
                color: getPolylineColor(tech.id),
                weight: 4,
              }}
            />
          )}
        </React.Fragment>
      ))}
    </MapContainer>
  );
}
