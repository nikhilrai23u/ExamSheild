<h2>Edit Student</h2>

<form action="{{ route('students.update', $student->id) }}" method="POST">
    @csrf
    @method('PUT')

    <input type="text" name="name" value="{{ $student->name }}"><br>
    <input type="email" name="email" value="{{ $student->email }}"><br>
    <input type="text" name="phone" value="{{ $student->phone }}"><br>
    <input type="text" name="course" value="{{ $student->course }}"><br>

    <button type="submit">Update</button>
</form>