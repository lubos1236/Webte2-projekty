import {
  Button,
  Dialog,
  DialogActions,
  DialogContent,
  DialogContentText,
  DialogTitle,
} from '@mui/material'
import { FormattedMessage } from 'react-intl'

export function ConfirmationDialog(props: {
  onClose: (value: boolean) => void
  open: boolean
  text: string
}) {
  const { onClose, open, text, ...other } = props

  const handleCancel = () => {
    onClose(false)
  }

  const handleOk = () => {
    onClose(true)
  }

  return (
    <Dialog maxWidth='sm' open={open} {...other}>
      <DialogTitle>
        <FormattedMessage id='common.confirmation' />
      </DialogTitle>
      <DialogContent>
        <DialogContentText>
          <FormattedMessage id={text} defaultMessage={text} />
        </DialogContentText>
      </DialogContent>
      <DialogActions>
        <Button autoFocus onClick={handleCancel}>
          <FormattedMessage id='common.no' />
        </Button>
        <Button onClick={handleOk}>
          <FormattedMessage id='common.yes' />
        </Button>
      </DialogActions>
    </Dialog>
  )
}
