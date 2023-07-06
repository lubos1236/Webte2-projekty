import { Backdrop, CircularProgress } from '@mui/material'
import React, { createContext, useContext, useState } from 'react'

interface LoadingContextProps {
  loading: boolean
  setLoading: React.Dispatch<React.SetStateAction<boolean>>
}

const LoadingContext = createContext<LoadingContextProps>({ loading: false, setLoading: () => {} })

export function LoadingProvider({ children }: { children: React.ReactNode }) {
  const [loading, setLoading] = useState(false)

  return (
    <LoadingContext.Provider value={{ loading, setLoading }}>
      {children}
      <Backdrop open={loading} sx={{ zIndex: (theme) => theme.zIndex.drawer + 1 }}>
        <CircularProgress color='inherit' />
      </Backdrop>
    </LoadingContext.Provider>
  )
}

export function useLoading() {
  const context = useContext(LoadingContext)

  if (!context) {
    throw new Error('useLoading must be used within a LoadingProvider')
  }

  return context
}
