import { Box } from '@mui/material'
import { Outlet } from 'react-router-dom'
import LocaleSelect from '@/components/LocaleSelect'
import React from 'react'

export default function DefaultLayout() {
  return (
    <Box sx={{ display: 'flex', flexDirection: 'column', height: '100dvh' }}>
      <Box
        component='main'
        sx={{
          backgroundColor: (theme) =>
            theme.palette.mode === 'light'
              ? theme.palette.grey[100]
              : theme.palette.grey[900],
          flexGrow: 1,
        }}>

        <LocaleSelect sx={{ position: 'fixed', right: 0, top: 0, mt: 2, mr: 2 }} />

        <Box maxWidth='lg' marginX='auto' marginY={1}>
          <Outlet />
        </Box>
      </Box>
    </Box>
  )
}
