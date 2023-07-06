import { AlertColor } from '@mui/material'
import { createContext } from 'react'
import { useSnackbar } from 'notistack'
import { useIntl } from 'react-intl'

interface SnackbarContextProps {
  triggerSnackbar: (message: string, severity: AlertColor) => void
}

const SnackbarContext = createContext<SnackbarContextProps>({ triggerSnackbar: () => {} })

export function SnackbarProvider({ children }: { children: React.ReactNode }) {
  const { formatMessage } = useIntl()
  const { enqueueSnackbar } = useSnackbar()

  const triggerSnackbar = (message: string, severity: AlertColor) => {
    enqueueSnackbar(formatMessage({ id: message }), {
      variant: severity,
    })
  }

  return (
    <SnackbarContext.Provider value={{ triggerSnackbar }}>
      {children}
    </SnackbarContext.Provider>
  )
}

export default SnackbarContext
