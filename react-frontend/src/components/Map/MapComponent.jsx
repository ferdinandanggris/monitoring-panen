import React, { useEffect, useRef } from 'react';
import Map from '../../utils/map';
import './MapComponent.css';
import 'leaflet.gridlayer.googlemutant';
import 'leaflet/dist/leaflet.css';
import { MapContainer, TileLayer, GeoJSON } from 'react-leaflet';


const MapComponent = ({width = '100%', height = '500px', points, onSendTotalPanen, onGetLuasPerPoin, mapObject}) => {
  const mapRef = useRef(null);
  const zoom = 20;
  const center = [-6.1753924, 106.8271528];

  const [mapHasRendered, setMapHasRendered] = React.useState(false);
  const sendTotalPanen = (total) => {
    onSendTotalPanen(total);
  }

  const sendLuasPerPoin = (luas) => {
    onGetLuasPerPoin(luas);
  }

  const pointToMap = (longitude, latitude) => {
    mapObject.pointToMap(longitude, latitude);
  }

  const geoJSONData = {
    type: "FeatureCollection",
    features: [
      {
        type: "Feature",
        properties: { name: "Area A" },
        geometry: {
          type: "Polygon",
          coordinates: [
            [
              [106.827153, -6.175392],
              [106.828, -6.175],
              [106.828, -6.176],
              [106.827153, -6.175392]  // Titik terakhir harus sama dengan titik awal untuk membentuk polygon tertutup
            ]
          ]
        }
      }
    ]
  }

  useEffect(() => {

    // mapObject?.initMap();
    // const map = mapObject?.map;

    // console.log('map : ' + map);  
    // points?.forEach((point) => {
    //   mapObject.pointToMap(point.longitude, point.latitude);
    // });

    // sendTotalPanen(mapObject.totalArea);
    // mapObject.removeMarker();

    // Membersihkan saat komponen di-unmount

    if (mapRef.current) {
      const googleLayer = L.gridLayer.googleMutant({
        type: 'satellite', // Pilihan: roadmap, satellite, terrain, atau hybrid
        maxZoom: 20,
        apiKey: 'YOUR_GOOGLE_MAPS_API_KEY', // Ganti dengan API Key Google Maps Anda
      });
      mapRef.current.addLayer(googleLayer);
    }
    
    return () => {
      // map?.remove();
    };
  },[points]);

  useEffect(() => {
    if (mapRef.current) {
      mapRef.current.setView(center, zoom);
    }
  }, [center, zoom])

  return (
    <MapContainer
      ref={mapRef}
      center={center}
      zoom={zoom}
      whenReady={(mapInstance) => {mapRef.current = mapInstance}}
      style={{ width, height }}
    >
      <TileLayer
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
      />

      <GeoJSON data={geoJSONData} />
    </MapContainer>
  );
};

export default MapComponent;