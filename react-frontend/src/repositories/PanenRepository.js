import { ref } from "firebase/database";
import { realtimeDB } from "../lib/firebase";

class PanenRepository {

  subscribePanen(){
    return ref(realtimeDB, 'maps-point');
  }
}

export default PanenRepository;