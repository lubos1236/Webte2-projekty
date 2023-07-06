import { createContext, useMemo, useState } from 'react'
import { IntlProvider } from 'react-intl'
import { AuthProvider } from '@/components/AuthProvider'
import { CssBaseline, ThemeProvider } from '@mui/material'
import { RouterProvider } from 'react-router-dom'
import router from '@/router'
import theme from '@/theme'
import messages, { common as commonMessages } from '@/locales'
import { SnackbarProvider } from './components/SnackbarProvider'
import { LoadingProvider } from './components/LoadingProvider'
import { LocalizationProvider } from '@mui/x-date-pickers'
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs'
import { SnackbarProvider as NotistackProvider } from 'notistack'

export const LocaleContext = createContext({
  locale: 'en',
  locales: ['en'] as Array<keyof typeof messages>,
  changeLocale: (locale: keyof typeof messages) => {},
})

const defaultLocale = localStorage.getItem('locale') as keyof typeof messages

export default function App() {
  const [locale, setLocale] = useState<keyof typeof messages>(defaultLocale || 'en')
  const locales = Object.keys(messages) as Array<keyof typeof messages>
  const currentMessages = useMemo(
    () => ({ ...commonMessages, ...messages[locale] }),
    [locale]
  )

  function changeLocale(locale: keyof typeof messages) {
    setLocale(locale)
    localStorage.setItem('locale', locale)
  }

  return (
    <ThemeProvider theme={theme}>
      <IntlProvider defaultLocale='en' locale={locale} messages={currentMessages}>
        <NotistackProvider maxSnack={3}>
          <SnackbarProvider>
            <AuthProvider>
              <LocalizationProvider dateAdapter={AdapterDayjs}>
                <LocaleContext.Provider value={{ locale, changeLocale, locales }}>
                  <LoadingProvider>
                    <CssBaseline />
                    <RouterProvider router={router} />
                  </LoadingProvider>
                </LocaleContext.Provider>
              </LocalizationProvider>
            </AuthProvider>
          </SnackbarProvider>
        </NotistackProvider>
      </IntlProvider>
    </ThemeProvider>
  )
}
