type Panen = {
  m_mesin_id : string;
  m_sopir_id : string;
  lokasi : GeoPoint;
}

type GeoPoint = {
  latitude : number;
  longitude : number;
}

export default Panen;