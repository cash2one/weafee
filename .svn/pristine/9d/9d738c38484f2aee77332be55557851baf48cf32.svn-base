function Queue() {
  this.tail = null;
  this.head = null;
  this.size = 0;
}

Queue.prototype.enqueue = function(obj) {
  newEntry = {
    value: obj,
    next: this.tail
  }
  if (this.tail) {
    this.tail.prev = newEntry;
  } else {
    this.head = newEntry;
  }
  this.tail = newEntry;
  this.size++;
}

Queue.prototype.dequeue = function() {

  if (this.head==null) {
    return null;
  }

  entry = this.head;
  obj = entry.value;

  if (this.head.prev) {
    this.head = this.head.prev;
    this.head.next = null;
  } else { // Last element
    this.head = null;
    this.tail = null;
  }

  delete(entry);
  this.size--;
  return obj;

}

Queue.prototype.sendToTail = function(value) {

  entry = this._findEntry(value);
  if (entry == null) {
    return false;
  }

  if (entry==this.tail) { // already
    return true;
  }

  if (entry.prev) {
    if (entry == this.head) {
      this.head = entry.prev;
    }
    entry.prev.next = entry.next;
  }

  if (entry.next) {
    entry.next.prev = entry.prev;
  }

  entry.prev = null;
  entry.next = this.tail;
  if (this.tail) {
    this.tail.prev = entry;
  }
  this.tail = entry;

  return true;

}

Queue.prototype.describe= function() {
  current = this.tail;
  description = "";
  while (current) {
    description += (description.length ? ", " : "");
    description += current.value;
    if (current==this.tail) {
      description+="[T]";
    }
    if (current==this.head) {
      description+="[H]";
    }
    current = current.next;
  }
  return this.size + " elements: '" + description + "'";
}

Queue.prototype._findEntry= function(value) {
   current = this.tail;
   while (current) {
      if (current.value == value) {
        return current;
      }
      current = current.next;
    }
    return null;
}


