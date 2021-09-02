import sys
import json
import smtplib

# Tags Cambios Interfaz: Design
# Tags Desarrollo: Development , Implement, Refactor
# Tags Pruebas: Test

# Al Finalizar semana
# Tags Comentarios en correo: Comment
# Tags Objetivos futuros: Target
# Tags Obstaculos: Obstacles

week_tasks = json.loads(sys.argv[1])

def buildMessage(week_tasks):
    message = ""
    developed_activities = "Actividades desarrolladas, objetivos logrados:\n"
    ui = "\n- Se implemento los siguientes cambios de interfaz:"
    bugs = "\n- Se arreglaron los siguientes bugs: "
    features = "- Se implemento las siguientes tareas/historias: "
    tests = "\n- Se hizo pruebas en las siguientes incidencias: "
    comments = "\n"
    future_goals = "\nObjetivos para la semana proxima:\n"
    needs = "\nNecesidades, pendientes que requieren de Direccion para seguir avanzando:\n"
    
    for task in week_tasks:
        tag = task['etiquetas']
        description = task['incidencia']
        if tag == "Development" or tag == "Refactor" or tag == "Implement":
            features += description + ", "
        elif tag == "Design":
            ui += description + ", "
        elif tag == "Debug":
            bugs += description + ", "
        elif tag == "Test":
            tests += description + ", "
        elif tag == "Target":
            future_goals += description + "\n"
        elif tag == "Obstacle":
            needs += "\n" + description + "\n"
        elif tag == "Comment":
            comments += "- "+ description + "\n"

    message = (developed_activities + 
            (features[:-2] if isEmpty(features) else "") + 
            (ui[:-2] if isEmpty(ui) else "") +
            (bugs[:-2] if isEmpty(bugs) else "") +
            (tests[:-2] if isEmpty(tests) else "") +
            comments +
            future_goals +
            needs)
    return message;        


def isEmpty (myString):
    return "," in myString


def sendEmail(message):
    sender = "juancarlos.aranda@hivisionled.red"
    recipient = "juancarlos.aral@gmail.com"
    password = "" 
    subject = "Reporte Semanal"
    text = message

    smtp_server = smtplib.SMTP_SSL("send.one.com", 465)
    smtp_server.login(sender, password)
    message = """From: %s\nTo: %s\nSubject: %s\n\n%s""" % (sender, recipient, subject, text)
    smtp_server.sendmail(sender, recipient, message)
    print("Se envio mensaje a: "+ recipient)
    smtp_server.close()


message = buildMessage(week_tasks)
sendEmail(message)
print(message)








