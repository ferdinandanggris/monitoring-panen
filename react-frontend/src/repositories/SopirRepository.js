import { getDocs, collection, query, where } from "firebase/firestore";
import { database } from "../lib/firebase";
class SopirRepository{
  async getSopir() {
    const querySnapshot = await getDocs(collection(database, "m_sopir"));
    const sopir = [];
    querySnapshot.forEach((doc) => {
      const data = doc.data();
      data.id = doc.id;
      sopir.push(data);
    });
    return sopir;
  }

  async getSopirById(id) {
    const querySnapshot = await getDocs(query(collection(database, "m_sopir"), where("id", "==", id)));
    let sopir= null;
    if (querySnapshot.size > 0) {
      querySnapshot.forEach((doc) => {
        const data = doc.data();
        
        data.id = doc.id;
        sopir = data;
      });
    }
    return sopir;
  }
}

export default SopirRepository;