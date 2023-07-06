import { Box, Button, CircularProgress, SxProps, Theme } from '@mui/material'
import { useContext, useState } from 'react'
import SnackbarContext from './SnackbarProvider'

interface Props {
  resourceCallback: () => Promise<Blob>,
  children?: React.ReactNode,
  filename?: string,
  sx?: SxProps<Theme>
}

export default function DownloadFileButton({ resourceCallback, children, filename, sx }: Props) {
  const [isDisabled, setIsDisabled] = useState(false)
  const { triggerSnackbar } = useContext(SnackbarContext)

  const downloadFile = async () => {
    const blob = await resourceCallback()

    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = filename ?? 'file'
    document.body.appendChild(a)
    a.click()
    a.remove()
  }

  const handleButtonClick = () => {
    setIsDisabled(true)
    downloadFile()
      .then(() => {
        triggerSnackbar('File downloaded successfully', 'success')
      })
      .catch((error) => {
        console.error(error)
        triggerSnackbar('Failed to download the file', 'error')
      })
      .finally(() => setIsDisabled(false))
  }

  return (
    <Box sx={{ position: 'relative', ...sx }}>
      <Button
        variant='outlined'
        disabled={isDisabled}
        onClick={handleButtonClick}
      >
        {children}
      </Button>
      {isDisabled && (
        <CircularProgress
          size={24}
          sx={{
            position: 'absolute',
            top: '50%',
            left: '50%',
            marginTop: '-12px',
            marginLeft: '-12px',
          }}
        />
      )}
    </Box>
  )
}
