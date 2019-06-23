var synth = window.speechSynthesis;
var voices = [];

// <b>1.</b> g2-g3 f7-f5<br><b>2.</b> g3-g4 f5:g4<br><b>3.</b> Gf1-h3 Sg8-h6<br><b>4.</b> Sg1-f3 Wh8-g8<br><b>5.</b> O-O b7-b6<br><b>6.</b> Gh3:g4 Gc8-b7<br><b>7.</b> Gg4:d7+ Ke8-f7<br><b>8.</b> b2-b3 Gb7:f3<br><b>9.</b> c2-c4

function speakLastChessNotation(rawMoves)
{
    var lastMove = extractLastMove(rawMoves);
    var translated = transalateChessNotation(lastMove);
    speak(translated);
}

function speak(input)
{
    if (synth.speaking)
    {
        console.error("TTS already speaking");
        return;
    }

    var speechHandler = new SpeechSynthesisUtterance(input);
    var voices = synth.getVoices();
    if (voices.length === 0)
    {
        console.error("No voices found");
        return;
    }
    speechHandler.voice = voices[0];
    speechHandler.pitch = 1;
    speechHandler.rate = 1;
    synth.speak(speechHandler);
}

function populateVoices()
{
    voices = synth.getVoices();
}
populateVoices();
if (speechSynthesis.onvoiceschanged !== undefined)
{
    speechSynthesis.onvoiceschanged = populateVoices();
}

function transalateChessNotation(notation)
{
    var parts = notation.split(" ");
    var current = getLastArrayElement(parts);
    var color = (parts.length === 2 ? "Black" : "White");
    var specialMove = getSpecialMove(notation, color);

    var final;
    if (specialMove === "none")
    {
        var isCheck = getCheck(notation);
        var figure = getFigure(notation[0]);
        var sourcePosition = getSourcePosition(notation).toUpperCase();
        var targetPosition = getTargetPosition(notation).toUpperCase();
        if (isCheck)
        {
            final = color + " " + figure + " check on " + targetPosition;
        }
        else
        {
            final = color + " " + figure + " moved to " + targetPosition + " from " + sourcePosition;
        }
    }
    else
    {
        final = specialMove;
    }

    return final;
}

function getCheck(notation)
{
    return notation.includes("+");
}

function getSpecialMove(notation, color)
{
    if (notation === "O-O")
    {
        return color + "short castling";
    }
    else if (notation === "O-O-O")
    {
        return color + "long castling";
    }
    else if (notation === "1-0")
    {
        return "White wins";
    }
    else if (notation === "0-1")
    {
        return "Black wins";
    }
    else if (notation === "0.5-0.5")
    {
        return "Match draw";
    }
    else if (notation.includes("#"))
    {
        return "Check mate";
    }

    return "none";
}

function getSourcePosition(notation)
{
    if (notation.includes("-"))
    {
        return notation.split("-")[0];
    }
    else if (notation.includes(":"))
    {
        return notation.split(":")[0];
    }
}

function getTargetPosition(notation)
{
    if (notation.includes("-"))
    {
        return notation.split("-")[1];
    }
    else if (notation.includes(":"))
    {
        return notation.split(":")[1];
    }
}

function getFigure(symbol)
{
    if (symbol === symbol.toLowerCase())
    {
        return "pawn";
    }

    switch(symbol)
    {
        case "K":
        {
            return "king";
        }
        case "H":
        {
            return "queen";
        }
        case "G":
        {
            return "bishop";
        }
        case "S":
        {
            return "knight";
        }
        case "W":
        {
            return "rock";
        }
        default:
        {
            return "unknown checker";
        }
    }
}

function extractLastMove(moves)
{
    var parts = moves.split("<br>");
    var lastPart = getLastArrayElement(parts);
    var rawNotationParts = lastPart.split("</b>");
    var rawNotation = getLastArrayElement(rawNotationParts);
    return rawNotation.trim();
}

function getLastArrayElement(array)
{
    return array.slice(-1)[0];
}