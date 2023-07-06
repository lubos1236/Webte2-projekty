import {
  Button,
  Checkbox,
  Divider,
  FormControl,
  FormHelperText,
  FormLabel,
  InputLabel,
  List,
  ListItem,
  ListItemButton,
  ListItemIcon,
  ListItemText,
  MenuItem,
  Paper,
  Select,
  Stack,
  TextField,
} from '@mui/material'
import { DateTimePicker } from '@mui/x-date-pickers/DateTimePicker'
import SnackbarContext from '@/components/SnackbarProvider'
import { ky } from '@/utils/ky'
import { useContext, useState } from 'react'
import { FormattedMessage, useIntl } from 'react-intl'
import { ResponseBody, Student } from '@/types/api'
import { useEffectOnce } from '@/hooks/useEffectOnce'
import { Dayjs } from 'dayjs'
import { Title } from '@/components/Title'

interface ExerciseSet {
  id: number
  file_name: string
  created_at: string
}

interface Errors {
  title?: string
  description?: string
  students?: string
  exerciseSet?: string
  points?: string
}

function validateForm(content: any): [boolean, Errors] {
  const errors: Errors = {}
  let isValid = true

  if (!content.title) {
    errors.title = 'assigning.form.errors.titleRequired'
    isValid = false
  }

  if (!content.description) {
    errors.description = 'assigning.form.errors.descriptionRequired'
    isValid = false
  }

  if (!content.studentIds.length) {
    errors.students = 'assigning.form.errors.studentsRequired'
    isValid = false
  }

  if (!content.exerciseSetIds?.length) {
    errors.exerciseSet = 'assigning.form.errors.exerciseSetRequired'
    isValid = false
  }

  if (typeof content.points !== 'number') {
    errors.points = 'assigning.form.errors.pointsRequired'
    isValid = false
  } else {
    if (content.points < 1) {
      errors.points = 'assigning.form.errors.pointsPositive'
      isValid = false
    }
  }

  return [isValid, errors]
}

export default function Assigning() {
  const intl = useIntl()
  const { triggerSnackbar } = useContext(SnackbarContext)
  const [exerciseSets, setExerciseSets] = useState<ResponseBody<ExerciseSet>>({
    items: [],
    total: 0,
  })
  const [students, setStudents] = useState<ResponseBody<Student>>({ items: [], total: 0 })

  // form data
  const [title, setTitle] = useState('')
  const [description, setDescription] = useState('')
  const [studentIds, setStudentIds] = useState<number[]>([])
  const [exerciseSetIds, setExerciseSetIds] = useState<number[]>([])
  const [points, setPoints] = useState<number | ''>('')
  const [startDate, setStartDate] = useState<Dayjs | null>(null)
  const [endDate, setEndDate] = useState<Dayjs | null>(null)

  const [errors, setErrors] = useState<Errors>({})

  useEffectOnce(() => {
    ky.get('exercises')
      .json<ResponseBody<ExerciseSet>>()
      .then(setExerciseSets)
      .catch(console.error)

    ky.get('students')
      .json<ResponseBody<Student>>()
      .then(setStudents)
      .catch(console.error)
  })

  const reset = () => {
    setTitle('')
    setDescription('')
    setStudentIds([])
    setExerciseSetIds([])
    setPoints('')
    setStartDate(null)
    setEndDate(null)
    setErrors({})
  }

  const handleSubmit = () => {
    const content = {
      title,
      description,
      studentIds,
      exerciseSetIds,
      points,
      startDate,
      endDate,
    }
    const [isValid, errors] = validateForm(content)
    setErrors(errors)

    if (!isValid) return

    const body = {
      title,
      description,
      student_ids: studentIds,
      exercise_set_ids: exerciseSetIds,
      max_points: points,
      start_date: startDate?.toISOString(),
      end_date: endDate?.toISOString(),
    }

    ky.post('assignment-groups', { json: body })
      .then(() => {
        reset()
        triggerSnackbar('assigning.snackbar.success', 'success')
      })
      .catch((err) => {
        console.error(err)
        triggerSnackbar('assigning.snackbar.error', 'error')
      })
  }

  const handleStudentListItemToggle = (student: Student) => {
    if (studentIds.includes(student.id)) {
      setStudentIds(studentIds.filter((id) => id !== student.id))
    } else {
      setStudentIds([...studentIds, student.id])
    }
  }

  return (
    <Paper sx={{ marginTop: 4 }}>
      <Title text='navbar.assigning' />
      <Stack direction={'column'} spacing={4} padding={4}>
        <Stack
          direction={{ xs: 'column', sm: 'row' }}
          spacing={4}
          justifyContent={'center'}>
          <Stack direction={'column'} spacing={4} sx={{ width: '100%' }}>
            <FormControl>
              <FormLabel error={!!errors.title} required>
                <FormattedMessage id='assigning.form.labels.title' />
              </FormLabel>
              <TextField
                error={!!errors.title}
                value={title}
                onChange={(event) => setTitle(event.target.value)}
              />
              {!!errors.title && (
                <FormHelperText error>
                  <FormattedMessage id={errors.title} />
                </FormHelperText>
              )}
            </FormControl>
            <FormControl>
              <FormLabel error={!!errors.description} required>
                <FormattedMessage id='assigning.form.labels.description' />
              </FormLabel>
              <TextField
                multiline
                rows={2}
                value={description}
                error={!!errors.description}
                onChange={(event) => setDescription(event.target.value)}
              />
              {!!errors.description && (
                <FormHelperText error>
                  <FormattedMessage id={errors.description} />
                </FormHelperText>
              )}
            </FormControl>
          </Stack>
          <FormControl sx={{ width: '100%' }}>
            <FormLabel error={!!errors.students} required>
              <FormattedMessage id='assigning.form.labels.students' />
            </FormLabel>
            <Paper variant='outlined'>
              <List sx={{ maxHeight: '200px', overflowY: 'auto' }}>
                {students.items.map((student) => (
                  <ListItem key={student.id} disablePadding>
                    <ListItemButton
                      onClick={() => handleStudentListItemToggle(student)}
                      dense>
                      <ListItemIcon>
                        <Checkbox
                          checked={studentIds.includes(student.id)}
                          edge='start'
                        />
                      </ListItemIcon>
                      <ListItemText
                        primary={`${student.first_name} ${student.last_name}`}
                        secondary={student.email}
                      />
                    </ListItemButton>
                  </ListItem>
                ))}
              </List>
            </Paper>
            {!!errors.students && (
              <FormHelperText error>
                <FormattedMessage id={errors.students} />
              </FormHelperText>
            )}
          </FormControl>
        </Stack>

        <Stack direction={'column'} spacing={4}>
          <FormControl>
            <InputLabel
              id='exercise-set-select-label'
              error={!!errors.exerciseSet}
              required>
              <FormattedMessage id='assigning.form.labels.exerciseSet' />
            </InputLabel>
            <Select
              id='exercise-set-select'
              labelId='exercise-set-select-label'
              value={exerciseSetIds}
              multiple
              sx={{ minWidth: '200px' }}
              onChange={(event) => setExerciseSetIds(event.target.value as number[])}
              label={intl.formatMessage({ id: 'assigning.form.labels.exerciseSet' })}
              error={!!errors.exerciseSet}>
              {exerciseSets.items.map((exerciseSet) => (
                <MenuItem key={exerciseSet.id} value={exerciseSet.id}>
                  {exerciseSet.file_name}
                </MenuItem>
              ))}
            </Select>
            {!!errors.exerciseSet && (
              <FormHelperText error>
                <FormattedMessage id={errors.exerciseSet} />
              </FormHelperText>
            )}
          </FormControl>

          <FormControl>
            <FormLabel error={!!errors.exerciseSet} required>
              <FormattedMessage id='assigning.form.labels.points' />
            </FormLabel>
            <TextField
              type='number'
              value={points}
              error={!!errors.points}
              onChange={(event) => setPoints(Number(event.target.value))}
            />
            {!!errors.points && (
              <FormHelperText error>
                <FormattedMessage id={errors.points} />
              </FormHelperText>
            )}
          </FormControl>
          <Divider />
          <Stack direction={{ xs: 'column', sm: 'row' }} spacing={4}>
            <FormControl sx={{ width: '100%' }}>
              <FormLabel>
                <FormattedMessage id='assigning.form.labels.startDate' />
              </FormLabel>
              <DateTimePicker
                value={startDate}
                onChange={(newValue) => setStartDate(newValue)}
              />
            </FormControl>
            <FormControl sx={{ width: '100%' }}>
              <FormLabel>
                <FormattedMessage id='assigning.form.labels.endDate' />
              </FormLabel>
              <DateTimePicker
                value={endDate}
                minDateTime={startDate || undefined}
                onChange={(newValue) => setEndDate(newValue)}
              />
            </FormControl>
          </Stack>
        </Stack>

        <Button variant='contained' onClick={handleSubmit}>
          <FormattedMessage id='assigning.form.labels.submit' />
        </Button>
      </Stack>
    </Paper>
  )
}
