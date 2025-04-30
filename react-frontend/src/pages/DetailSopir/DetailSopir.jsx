import React, { useEffect } from 'react'
import Grid from "@mui/material/Grid2";
import { Avatar, Box, List, ListItem, ListItemAvatar, ListItemText} from "@mui/material";
import FolderIcon from "@mui/icons-material/Folder";
import PanenRepository from '../../repositories/PanenRepository';
import { get, onValue } from 'firebase/database';
import MapComponent from '../../components/Map/MapComponent';
import { Link, useParams } from 'react-router-dom';
import SopirRepository from '../../repositories/SopirRepository';
import TableComponent from '../../components/Table/TableComponent';
import RemoveRedEyeRoundedIcon from '@mui/icons-material/RemoveRedEyeRounded';
import Map from '../../utils/map';
import { AdapterMoment } from '@mui/x-date-pickers/AdapterMoment'
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { DatePicker } from '@mui/x-date-pickers';
import DateRangeComponent from '../../components/DateRange/DateRangeComponent';
import { onSnapshot } from 'firebase/firestore';
import moment from 'moment';


export default function DetailSopir() {
  const [points, setPoints] = React.useState([]);
  const [sopir, setSopir] = React.useState(null);
  const [DateRange, setDateRange] = React.useState({startDate : moment("2024-10-27 00:00:00"), endDate : moment("2024-10-29 23:59:59")});
  const [dataTablePoin, setDataTablePoin] = React.useState({columns : [], data : []});
  const [totalPanen, setTotalPanen] = React.useState(0);
  const mapObject = new Map();
  const { id } = useParams();

  const getSopir = async (id) => {
    const SopirRepositoryitory = new SopirRepository();
    const sopir = await SopirRepositoryitory.getSopirById(id);
    console.log('sopir : ' + JSON.stringify(sopir));
    setSopir(sopir);
  }

  const handleTotalPanenFromChild = (total) => {
    setTotalPanen(total);
  }
  
  const parseDatatablePoin = (datas) => {
    const datatables = datas.map((data) => {
      return [
        {
          id : 1,
          value : data.id,
          props : {
            align : 'center'
          }
        },
        {
          id : 2,
          value : `${data.latitude}, ${data.longitude}`,
          props : {
            align : 'center'
          }
        },
        {
          id : 3,
          value : moment(data.date).format('DD MMMM YYYY HH:mm:ss'),
          props : {
            align : 'center'
          }
        }
      ]
    });
    
    const columns = [
      {
        key : 1,
        name : 'ID Point',
        props : {
          align : 'center'
        }
      },
      {
        key : 2,
        name : 'Titik Point',
        props : {
          align : 'center'
        }
      },
      {
        key : 3,
        name : 'Waktu',
        props : {
          align : 'center'
        }
      }
    ];

    return {
      columns : columns,
      data : datatables
    }
  }

  const handleDateChange = (dates) => {
    setDateRange(dates);
  };
  
  

  useEffect(() => {
    const panenRepository = new PanenRepository();
    const panenCollection = panenRepository.subscribePanen();

    const unsubscribe = onValue(panenCollection, (snapshot) => {
      if(snapshot.exists()){
        const data = snapshot.val();
        const pointsArray = Object.keys(data).map(key => ({
          id: key,
          sopir_id : data[key].sopir_id,
          latitude: data[key].lat,
          longitude: data[key].long,
          date: data[key].date,
        }));

        // get only data from date range date (moment js)
        const startDate = new Date(DateRange.startDate);
        const endDate = new Date(DateRange.endDate);


        const filteredPoints = pointsArray.filter(point => {
          const pointDate = new Date(point.date);
          console.log('pointDate : ' + pointDate);
          console.log('startDate : ' + startDate);
          console.log('endDate : ' + endDate);
          return pointDate >= startDate && pointDate <= endDate && point.sopir_id === id;
        });

        console.log('filteredPoints : ' + JSON.stringify(filteredPoints));
        setPoints(filteredPoints);



        setDataTablePoin(parseDatatablePoin(filteredPoints));
      }
    });


    getSopir(id);

   return () => {
    unsubscribe();
  };
  },[DateRange]);


  return (
    <div>
    <Grid container spacing={2}>
        <Grid item size={{xs : 12, md : 12}} >
          <LocalizationProvider dateAdapter={AdapterMoment}>
              <DateRangeComponent onDateChange={handleDateChange} defaultDateRange={DateRange} />
            </LocalizationProvider>
        </Grid>
        <Grid item size={{xs : 12, md : 8}}  width={'100%'}>
            <MapComponent width="100%" height="500px" points={points} onSendTotalPanen={handleTotalPanenFromChild} mapObject={mapObject} />
        </Grid>
        <Grid item size={{xs : 12, md : 4}} >
          <div style={{background : '#eee',borderRadius: '10px'}} >
            <List dense={true}>
                <ListItem>
                  <ListItemAvatar>
                    <Avatar>
                      <FolderIcon />  
                    </Avatar>
                  </ListItemAvatar>
                  <ListItemText
                    primary="Nama Sopir"
                  secondary={ sopir?.nama }
                  />
                </ListItem>
                <ListItem>
                  <ListItemAvatar>
                    <Avatar>
                      <FolderIcon />  
                    </Avatar>
                  </ListItemAvatar>
                  <ListItemText
                    primary="Tanggal"
                  secondary={ moment(DateRange.startDate).format('DD MMMM YYYY') + ' - ' + moment(DateRange.endDate).format('DD MMMM YYYY')
                   }
                  />
                </ListItem>
                <ListItem>
                  <ListItemAvatar>
                    <Avatar>
                      <FolderIcon />  
                    </Avatar>
                  </ListItemAvatar>
                  <ListItemText
                    primary="Total Panen"
                  secondary={<>{totalPanen.toFixed(2)} m<sup>2</sup>  </> }
                  />
                </ListItem>
            </List>
          </div>
        </Grid>
        <Grid item size={{xs : 12, md : 12}} width={'100%'}>
            <TableComponent datas={dataTablePoin}  />
        </Grid>
      </Grid>

    </div>
  )
}
