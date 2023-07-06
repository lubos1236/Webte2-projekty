import { Box } from '@mui/material'
import { Outlet } from 'react-router-dom'
import Navbar from '@/components/Navbar'
import Copyright from '@/components/Copyright'

export default function GuestLayout() {
  return (
    <Box sx={{ display: 'flex', flexDirection: 'column', height: '100dvh' }}>
      <Navbar />

      <Box
        component='main'
        sx={{
          backgroundColor: (theme) =>
            theme.palette.mode === 'light'
              ? theme.palette.grey[100]
              : theme.palette.grey[900],
          flexGrow: 1,
        }}>
        <Box maxWidth='lg' marginX='auto' marginY={1}>
          <Outlet />
        </Box>
      </Box>

      <Copyright />
    </Box>
  )
}
