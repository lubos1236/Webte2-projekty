import { Box, IconButton, Menu, MenuItem } from '@mui/material'
import IconTranslate from '@mui/icons-material/Translate'
import { FormattedMessage } from 'react-intl'
import React, { ComponentProps, useContext, useState } from 'react'
import { LocaleContext } from '@/App'

export default function LocaleSelect(props: ComponentProps<typeof Box>) {
  const { locales, locale, changeLocale } = useContext(LocaleContext)

  const [anchorElLocale, setAnchorElLocale] = useState<null | HTMLElement>(null)

  const handleOpenLocaleMenu = (event: React.MouseEvent<HTMLElement>) => {
    setAnchorElLocale(event.currentTarget)
  }

  const handleCloseLocaleMenu = () => {
    setAnchorElLocale(null)
  }

  const handleChangeLocale = (locale: any) => {
    changeLocale(locale)
    handleCloseLocaleMenu()
  }

  return (
    <Box {...props}>
      <IconButton onClick={handleOpenLocaleMenu}>
        <IconTranslate />
      </IconButton>

      <Menu
        sx={{ mt: '45px' }}
        anchorEl={anchorElLocale}
        open={Boolean(anchorElLocale)}
        onClose={handleCloseLocaleMenu}
        anchorOrigin={{
          vertical: 'top',
          horizontal: 'right',
        }}
        keepMounted
        transformOrigin={{
          vertical: 'top',
          horizontal: 'right',
        }}>
        {locales.map((locale) => (
          <MenuItem key={locale} onClick={() => handleChangeLocale(locale)}>
            <FormattedMessage id={`lang.${locale}`} />
          </MenuItem>
        ))}
      </Menu>
    </Box>
  )
}
