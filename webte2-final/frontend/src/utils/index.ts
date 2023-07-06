export const getColorForPoints = (points: any) => {
  if (points === null) {
    return 'primary'
  }
  return points > 0 ? 'success.main' : 'error'
}
