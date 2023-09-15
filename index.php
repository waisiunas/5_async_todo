<?php require_once './partials/session.php' ?>

<?php
if (!isset($_SESSION['user'])) {
	header('location: ./login.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<?php
$title = "Tasks";
require_once './partials/head.php';
?>

<body>
	<div class="wrapper">
		<?php require_once './partials/sidebar.php' ?>

		<div class="main">
			<?php require_once './partials/topbar.php' ?>

			<main class="content">
				<div class="container-fluid p-0">
					<h1 class="h3 mb-3">Tasks</h1>
					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-body">
									<h3 class="text-center">Add Task</h3>
									<div id="alert"></div>
									<form id="add-form">
										<div class="row">
											<div class="col-md">
												<input type="text" class="form-control" name="task-input" id="task-input" placeholder="Please enter the task!">
											</div>
											<div class="col-md-auto">
												<input type="submit" value="Add" class="btn btn-primary">
											</div>
										</div>
									</form>
								</div>

								<div class="card-body">
									<h5>Tasks</h5>
									<div id="tasks">
										<!-- <div class="row mb-2">
											<div class="col-md">
												<input type="text" class="form-control" id="task-" value="Database Value" placeholder="Please enter the task!" readonly>
											</div>
											<div class="col-md-auto">
												<button class="btn btn-info" id="edit-" onclick="editTask(1)">Edit</button>
											</div>
											<div class="col-md-auto">
												<button class="btn btn-danger" id="delete-" onclick="editTask(1)">Delete</button>
											</div>
										</div> -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</main>

			<?php require_once './partials/footer.php' ?>
		</div>
	</div>

	<script src="./assets/js/app.js"></script>
	<script>
		showTasks();

		const alertElement = document.querySelector("#alert");
		const addFormElement = document.querySelector("#add-form");

		addFormElement.addEventListener("submit", function(e) {
			e.preventDefault();

			const taskInputElement = document.querySelector("#task-input");

			let taskInputValue = taskInputElement.value;

			if (taskInputValue == "") {
				taskInputElement.classList.add("is-invalid");
				alertElement.innerHTML = alertMaker('danger', 'Enter the task!');
			} else {
				taskInputElement.classList.remove("is-invalid");
				alertElement.innerHTML = "";

				const data = {
					body: taskInputValue,
					submit: 1,
				};

				fetch("./add-task.php", {
						method: 'POST',
						body: JSON.stringify(data),
						headers: {
							'Content-Type': 'application.json'
						},
					})
					.then(function(response) {
						return response.json();
					})
					.then(function(result) {
						if (result.errorBody) {
							taskInputElement.classList.add("is-invalid");
							alertElement.innerHTML = alertMaker('danger', result.errorBody);
						} else if (result.failure) {
							alertElement.innerHTML = alertMaker('danger', result.failure);
						} else if (result.success) {
							alertElement.innerHTML = alertMaker('success', result.success);
							taskInputElement.value = '';
							showTasks();
						} else {
							alertElement.innerHTML = alertMaker('danger', 'Something went wrong!');
						}
					});
			}
		});

		function showTasks() {
			fetch("./show-tasks.php")
				.then(function(response) {
					return response.json();
				})
				.then(function(result) {
					const tasksElement = document.querySelector("#tasks");
					let tasksRows = "";
					if (result.length > 0) {
						result.forEach(function(task) {
							tasksRows += `<div class="row mb-2">
											<div class="col-md">
												<input type="text" class="form-control" id="task-${task.id}" value="${task.body}" placeholder="Please enter the task!" readonly>
											</div>
											<div class="col-md-auto">
												<button class="btn btn-info" id="edit-${task.id}" onclick="editTask(${task.id})">Edit</button>
											</div>
											<div class="col-md-auto">
												<button class="btn btn-danger" id="delete-${task.id}" onclick="deleteTask(${task.id})">Delete</button>
											</div>
										</div>`
						});
					} else {
						tasksRows = `<div class="alert alert-info m-0" id="alert-msg">No record found!</div>`;
					}

					tasksElement.innerHTML = tasksRows;
				});
		}

		function editTask(id) {
			const editInputElement = document.querySelector(`#task-${id}`);
			const editBtnElement = document.querySelector(`#edit-${id}`);

			let editInputValue = editInputElement.value;
			let length = editInputValue.length;

			if (editBtnElement.innerText == "Edit") {
				editBtnElement.innerText = "Save";
				editInputElement.removeAttribute("readonly");
				editInputElement.focus();
				editInputElement.setSelectionRange(length, length);
			} else {
				if (editInputValue == "") {
					editInputElement.classList.add("is-invalid");
				} else {
					const data = {
						body: editInputValue,
						id: id,
						submit: 1,
					};

					fetch("./edit-task.php", {
							method: "POST",
							body: JSON.stringify(data),
							headers: {
								'Content-Type': 'application.json'
							},
						})
						.then(function(response) {
							return response.json();
						})
						.then(function(result) {
							editInputElement.classList.remove("is-invalid");

							if (result.errorBody) {
								editInputElement.classList.add("is-invalid");
							} else if (result.failure) {
								alertElement.innerHTML = alertMaker('danger', result.failure);
							} else if (result.success) {
								alertElement.innerHTML = alertMaker('success', result.success);
								editBtnElement.innerText = "Edit";
								editInputElement.addAttribute("readonly", true);
							} else {
								alertElement.innerHTML = alertMaker('danger', 'Something went wrong!');
							}
						});
				}

			}
		}

		function deleteTask(id) {
			const data = {
				id: id,
				submit: 1,
			};

			fetch("./delete-task.php", {
					method: 'POST',
					body: JSON.stringify(data),
					headers: {
						'Content-Type': 'application.json'
					}
				})
				.then(function(response) {
					return response.json();
				})
				.then(function(result) {
					if (result.failure) {
						alertElement.innerHTML = alertMaker('danger', result.failure);
					} else if (result.success) {
						alertElement.innerHTML = alertMaker('success', result.success);
						showTasks();
					} else {
						alertElement.innerHTML = alertMaker('danger', 'Something went wrong!');
					}
				})
		}

		function alertMaker(cls, msg) {
			return `<div class="alert alert-${cls} alert-dismissible fade show" role="alert">${msg}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
		}
	</script>

</body>

</html>