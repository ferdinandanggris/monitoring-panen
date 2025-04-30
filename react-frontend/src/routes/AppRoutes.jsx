import React from 'react'
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Frame from '../components/Frame/Frame';
import Dashboard from '../pages/Dashboard/Dashboard';
import Sopir from '../pages/Sopir/Sopir';
import DetailSopir from '../pages/DetailSopir/DetailSopir';
export default function AppRoutes() {
  return (
    <Router>
      <Frame>
        <Routes>
          <Route path="/" element={<Dashboard />} />
          <Route path="/sopir" element={<Sopir />} />
          <Route path="/sopir/:id" element={<DetailSopir />} />
        </Routes>
      </Frame>
    </Router>
  )
}
