import React from "react";
import { Navigate, Route, Routes } from "react-router-dom";
import Sidebar from "./pages/Layout/Sidebar";
import NotFound from "./pages/NotFound";
import Dashboard from "./pages/Dashboard/Dashboard";
import MasterDriverPage from "./pages/MasterDriverPage";
import MasterMachinePage from "./pages/MasterMachinePage";
function App() {
  return (
    <Routes>
      <Route element={<Sidebar />}>
        <Route path="/" element={<Navigate to="/dashboard" />} />
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/master/drivers" element={<MasterDriverPage />} />
        <Route path="/master/machines" element={<MasterMachinePage />} />
        {/* <Route path="/master/mesin" element={<MachineList />} />
        <Route path="/master/driver" element={<DriverList />} /> */}
      </Route>
      <Route path="*" element={<NotFound />} />
    </Routes>
  );
}

export default App;
