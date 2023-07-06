import { LocaleContext } from '@/App'
import { AuthContext } from '@/components/AuthProvider'
import DownloadFileButton from '@/components/DownloadFileButton'
import { useLoading } from '@/components/LoadingProvider'
import { Title } from '@/components/Title'
import { ky } from '@/utils/ky'
import { Roles } from '@/utils/roles'
import { Container, Stack, Typography } from '@mui/material'
import { useContext, useEffect, useState } from 'react'
import { FormattedMessage } from 'react-intl'
import ReactMarkdown from 'react-markdown'

function getMarkdownUri(role: Roles|undefined) {
  switch (role) {
    case Roles.Admin:
      return 'docs/admin'
    case Roles.Teacher:
      return 'docs/teacher'
    case Roles.Student:
      return 'docs/student'
  }
  return null
}

export default function UserGuide() {
  const auth = useContext(AuthContext)
  const { locale } = useContext(LocaleContext)
  const [markdown, setMarkdown] = useState<string|null>(null)
  const { loading, setLoading } = useLoading()

  const uri = getMarkdownUri(auth.user?.role)

  useEffect(() => {
    if (uri === null) {
      return
    }

    setLoading(true)
    ky.get(uri, { headers: { Accept: 'text/markdown' }, searchParams: { lang: locale } })
      .text()
      .then(setMarkdown)
      .finally(() => setLoading(false))
  }, [locale])

  const downloadPdfFile = async () => {
    if (uri === null) {
      throw new Error('Cannot download PDF file')
    }
    const headers = { accept: 'application/pdf' }
    const response = await ky.get(uri, { headers, searchParams: { lang: locale } })
    return await response.blob()
  }

  return (
    <Container>
      <Title text='navbar.guide' />
      <Stack
        direction='column'
        spacing={2}
        marginY={4}
      >
        <DownloadFileButton
          resourceCallback={downloadPdfFile}
          filename='user-guide.pdf'
          sx={{ alignSelf: 'end' }}
        >
          <FormattedMessage id='guide.labels.button.export.pdf' />
        </DownloadFileButton>
        {!loading && (markdown ?  <ReactMarkdown>{markdown}</ReactMarkdown> :
          <Typography variant='h4' align='center'>Guide is not available</Typography>)}
      </Stack>
    </Container>
  )
}
