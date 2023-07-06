import { createContext, ReactNode, useState } from 'react'
import { ky, setDefaults } from '@/utils/ky'
import { useEffectOnce } from '@/hooks/useEffectOnce'
import { Roles } from '@/utils/roles'
import { Box, Typography } from '@mui/material'

export interface AuthContextType {
  user: User | null
  initializing: boolean
  handleLogin: (token: string) => void
  handleLogout: () => void
}

export interface User {
  id: number
  email: string
  first_name: string
  last_name: string
  role: Roles
}

export const AuthContext = createContext<AuthContextType>({} as AuthContextType)

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null)
  const [initializing, setInitializing] = useState(true)

  useEffectOnce(() => {
    ky.post('auth/restore', { credentials: 'include' })
      .json()
      .then(async (data: any) => {
        handleLogin(data.access_token)
      })
      .catch(() => {})
      .finally(() => {
        setInitializing(false)
      })
  })

  function handleLogin(token: string) {
    const parts = token.split('.')
    const payload = JSON.parse(atob(parts[1]))

    setDefaults({
      headers: {
        authorization: `Bearer ${token}`,
      },
    })

    setUser({
      id: payload.sub,
      email: payload.email,
      first_name: payload.first_name,
      last_name: payload.last_name,
      role: payload.role,
    })
  }

  function handleLogout() {
    ky.post('auth/logout', { credentials: 'include' }).then(() => {
      setUser(null)
      setDefaults({
        headers: {
          authorization: undefined,
        },
      })
    })
  }

  const value = {
    initializing,
    user,
    handleLogin,
    handleLogout,
  }

  return (
    <AuthContext.Provider value={value}>
      {!value.initializing ? (
        children
      ) : (
        <Box
          sx={{
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            height: '100vh',
            width: '100vw',
            bgcolor: 'background.default',
            position: 'fixed',
            top: 0,
            left: 0,
          }}>
            <Typography color='text.primary'>
              Loading...
            </Typography>
        </Box>
      )}
    </AuthContext.Provider>
  )
}
