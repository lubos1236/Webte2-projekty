import kyclient, { Options } from 'ky'

const defaultOptions = {
  prefixUrl: import.meta.env.VITE_API_URL,
  headers: {
    accept: 'application/json',
  },
}

export let ky = kyclient.create({ ...defaultOptions })

export function setDefaults(options: Options) {
  ky = ky.extend(options)
}
