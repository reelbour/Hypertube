#include <stdlib.h>
#include <unistd.h>

typedef enum e_datatype
  {List, Dict}
datatype;

typedef enum e_keyval
  {First, Key, Val}
keyval;

typedef struct s_node {
  struct s_node *next;
  datatype dt;
  keyval kv;
} node;

node* add(datatype dt, node *list) {
  node* newnode;
  if (!(newnode = malloc(sizeof(node))))
    return NULL;

  newnode->next = list;
  newnode->dt = dt;
  newnode->kv = First;

  return newnode;
}

node* rm(node *list) {
  if (list == NULL)
    return NULL;

  node* res;
  res = list->next;
  free(list);

  return res;
}

void insertcoma(node **state) {
  switch ((*state)->dt)
  {
    case Dict:
      switch ((*state)->kv)
      {
        case First:
          (*state)->kv = Key;
          break;

        case Key:
          (*state)->kv = Val;
          write(1, ":", 1);
          break;

        case Val:
          (*state)->kv = Key;
          write(1, ",", 1);
          break;
      }
      break;

    case List:
      if ((*state)->kv == First)
        (*state)->kv = Val;
      else
        write(1, ",", 1);
      break;
  }
}

int open(const char **run, node **state) {
  if (!**run) return 0;

  switch (**run)
  {
    case 'd':
      if (*state) insertcoma(state);
      write(1, "{", 1);
      *state = add(Dict, *state);
      if (!**run) return 1;
      break;

    case 'l':
      if (!*state) return 2;
      insertcoma(state);
      write(1, "[", 1);
      *state = add(List, *state);
      if (!**run) return 3;
      break;

    case 'i':
      if (!*state) return 4;
      insertcoma(state);
      (*run)++;
      while (**run <= '9' && **run >= '0')
      {
        write(1, *run, 1);
        (*run)++;
      }
      if (**run != 'e') return 5;
      break;

    case 'e':
      if (!*state) return 6;
      if ((*state)->dt == Dict && (*state)->kv == Key)
        return 12;
      if ((*state)->dt == Dict)
        write(1, "}", 1);
      else if ((*state)->dt == List)
        write(1, "]", 1);
      else return 7;
      *state = rm(*state);
      break;

    default:
      if (!*state) return 8;
      if (**run <= '9' && **run >= '0')
      {
        int strlen = 0;

        while (**run <= '9' && **run >= '0')
        {
          strlen = (strlen * 10) + **run - '0';
          (*run)++;
        }
        if (**run != ':') return 9;
        (*run)++;

        insertcoma(state);
        write(1, "\"", 1);
        write(1, *run, strlen);
        write(1, "\"", 1);
        *run += strlen -1;
      }
      else return 10;
      break;
  }

  (*run)++;
  return open(run, state);
}

int main(int argc, char const *argv[]) {
  if (argc != 2) return 11;

  const char* run = argv[1];
  node* state = NULL;

  return open(&run, &state);
}
