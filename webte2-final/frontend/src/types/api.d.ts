export interface ResponseBody<T> {
  items: T[],
  total: number
}

export interface Teacher {
  id: number
  first_name: string
  last_name: string
  email: string
}

export interface AssignmentGroup {
  id: number
  title: string
  description: string
  start_date: string
  end_date: string | null
  max_points: string
  teacher_id: number
  teacher: Teacher
  created_at: string
  assignments: Assignment[]
}

export interface Assignment {
  id: number
  max_points: string
  exercise_set_id: number
  submissions: Submission[]
}

export interface Exercise {
  id: number
  task: string
  solution?: string
}

export interface Submission {
  id: number
  assignment_id: number
  student_id: number
  exercise_id: number
  points: string | null
  provided_solution: string | null
  assignment: Assignment
  exercise: Exercise | null
}

export interface Student {
  id: number
  first_name: string
  last_name: string
  email: string
}
