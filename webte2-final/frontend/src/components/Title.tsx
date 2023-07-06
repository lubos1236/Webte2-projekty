import { useEffect } from 'react'
import { useIntl } from 'react-intl'

export function Title({
  text,
  noTranslate = false,
}: {
  text?: string
  noTranslate?: boolean
}) {
  const { formatMessage } = useIntl()

  useEffect(() => {
    let title = 'Pengu'

    if (text) {
      title = noTranslate ? text : formatMessage({ id: text, defaultMessage: text })
      title += ' | Pengu'
    }

    document.title = title
  }, [text, noTranslate])

  return null
}
