// GeoJSON batas wilayah 8 Kecamatan di Kota Palu, Sulawesi Tengah
// Sumber referensi: BPS Kota Palu & Pemetaan Spasial Dispora Palu
export const paluDistrictsGeoJSON = {
  type: "FeatureCollection",
  features: [
    {
      type: "Feature",
      id: 1,
      properties: {
        id: 1,
        name: "Palu Barat",
        center: [-0.8650, 119.8520],
        population: 64200
      },
      geometry: {
        type: "Polygon",
        coordinates: [[
          [119.842, -0.855],
          [119.862, -0.855],
          [119.868, -0.875],
          [119.848, -0.878],
          [119.840, -0.868],
          [119.842, -0.855]
        ]]
      }
    },
    {
      type: "Feature",
      id: 2,
      properties: {
        id: 2,
        name: "Palu Timur",
        center: [-0.8750, 119.8820],
        population: 78500
      },
      geometry: {
        type: "Polygon",
        coordinates: [[
          [119.868, -0.855],
          [119.895, -0.858],
          [119.898, -0.885],
          [119.872, -0.888],
          [119.868, -0.875],
          [119.868, -0.855]
        ]]
      }
    },
    {
      type: "Feature",
      id: 3,
      properties: {
        id: 3,
        name: "Palu Selatan",
        center: [-0.9020, 119.8750],
        population: 75300
      },
      geometry: {
        type: "Polygon",
        coordinates: [[
          [119.865, -0.888],
          [119.898, -0.885],
          [119.895, -0.920],
          [119.860, -0.918],
          [119.865, -0.888]
        ]]
      }
    },
    {
      type: "Feature",
      id: 4,
      properties: {
        id: 4,
        name: "Palu Utara",
        center: [-0.8250, 119.8730],
        population: 26100
      },
      geometry: {
        type: "Polygon",
        coordinates: [[
          [119.855, -0.805],
          [119.890, -0.810],
          [119.888, -0.845],
          [119.852, -0.840],
          [119.855, -0.805]
        ]]
      }
    },
    {
      type: "Feature",
      id: 5,
      properties: {
        id: 5,
        name: "Mantikulore",
        center: [-0.8880, 119.9140],
        population: 81200
      },
      geometry: {
        type: "Polygon",
        coordinates: [[
          [119.895, -0.858],
          [119.940, -0.865],
          [119.942, -0.925],
          [119.895, -0.920],
          [119.898, -0.885],
          [119.895, -0.858]
        ]]
      }
    },
    {
      type: "Feature",
      id: 6,
      properties: {
        id: 6,
        name: "Ulujadi",
        center: [-0.8540, 119.8350],
        population: 36400
      },
      geometry: {
        type: "Polygon",
        coordinates: [[
          [119.815, -0.835],
          [119.845, -0.840],
          [119.848, -0.878],
          [119.818, -0.872],
          [119.815, -0.835]
        ]]
      }
    },
    {
      type: "Feature",
      id: 7,
      properties: {
        id: 7,
        name: "Tatanga",
        center: [-0.9210, 119.8600],
        population: 53800
      },
      geometry: {
        type: "Polygon",
        coordinates: [[
          [119.840, -0.878],
          [119.865, -0.888],
          [119.860, -0.940],
          [119.835, -0.935],
          [119.840, -0.878]
        ]]
      }
    },
    {
      type: "Feature",
      id: 8,
      properties: {
        id: 8,
        name: "Tawaeli",
        center: [-0.7740, 119.8920],
        population: 23500
      },
      geometry: {
        type: "Polygon",
        coordinates: [[
          [119.865, -0.755],
          [119.915, -0.760],
          [119.910, -0.805],
          [119.860, -0.800],
          [119.865, -0.755]
        ]]
      }
    }
  ]
};
